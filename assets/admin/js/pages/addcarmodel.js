if(ACTION==0){
  $("#carmodel").select2({    
    allowClear: true,
    minimumInputLength: 2,              
    tokenSeparators: [','],
    createSearchChoice: function(term, data) {
      if ($(data).filter(function() {
        return this.text.localeCompare(term) === 0;
        }).length === 0) {
          if(term.match(/^[a-zA-Z0-9- ]+$/g))
            return {id: term,text: term};
        }
    },
    tags:true,
    
    });
}  
function resetdata(){

  $("#carbrand_div").removeClass("has-error is-focused");
  $("#carmodel_div").removeClass("has-error is-focused");

  if(ACTION==1){
    
  }else{
    $('#carbrandid').val('0');
    $("#carmodel").select2("val", "0");
    $('#s2id_carmodel > ul').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});
    $('#yes').prop("checked", true);
    $('#carbrand').focus();
  }
  $('#carbrandid').selectpicker('refresh');
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var carbrandid = $("#carbrandid").val().trim();
  var carmodel = $("#carmodel").val();
  
  var isvalidcarbrandid = isvalidcarmodel = 0;
  
  PNotify.removeAll();
  if(carbrandid == 0){
    $("#carbrand_div").addClass("has-error is-focused");
    new PNotify({title: 'Please select car brand !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcarbrandid = 0;
  }else { 
    isvalidcarbrandid = 1;
  }
  if(ACTION==0){
    if(carmodel.trim() == 0 || carmodel.split(',').length < 1){
      $("#carmodel_div").addClass("has-error is-focused");
      $('#s2id_carmodel > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
      new PNotify({title: 'Please enter car model name !',styling: 'fontawesome',delay: '3000',type: 'error'});  
      isvalidcarmodel = 0;
    }else { 
      isvalidcarmodel = 1;
      $('#s2id_carmodel > ul').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});
    }
  }else{
    if(carmodel == ''){
      $("#carmodel_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter car model name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcarmodel = 0;
    }else {
      if(carmodel.length<3){
        $("#carmodel_div").addClass("has-error is-focused");
        new PNotify({title: 'Car model name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcarmodel = 0;
      }else{
        isvalidcarmodel = 1;
      }
    }
  }
  

  if(isvalidcarbrandid == 1 && isvalidcarmodel == 1){

    var formData = new FormData($('#carmodelform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"carmodel/addcarmodel";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        datatype:'json',
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          response =  $.parseJSON(response);
          if(response['error']==1){
            new PNotify({title: "Car Model successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response['error']==2){
            new PNotify({title: response['data']+" already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $('#s2id_carmodel > ul').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
          }else{
            new PNotify({title: 'Car Model not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"carmodel/updatecarmodel";
      
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
              new PNotify({title: "Car Model successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"carmodel"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Car Model already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Car Model not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

