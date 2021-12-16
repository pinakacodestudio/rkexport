$(document).ready(function() {

    $('#todate').datetimepicker({
        todayHighlight: true,
        format: 'd-m-Y H:i:s',
        autoclose: true,
        todayBtn:"linked",
        orientation:"bottom"
    });
});

function resetdata(){
    $("#mobileno_div").removeClass("has-error is-focused");
    $("#mobilekey_div").removeClass("has-error is-focused");
    $("#status_div").removeClass("has-error is-focused");
          
    if(ACTION==0){     
      $('#mobileno').val('');
      $('#mobilekey').val('');       
      $('.selectpicker').selectpicker('refresh');    
    }
}

function checkvalidation(){ 
       
    var mobileno = $("#mobileno").val().trim();
    var mobilekey = $("#mobilekey").val().trim();
    var synchronize = document.getElementById('synchronize');
    var forwardemployee = $("#forwardemployee").val().trim();
    var backwardemployee = $("#backwardemployee").val().trim();
    
    var todate = $("#todate").val().trim();
    //var radio = document.getElementById("enable").value;
    
    var isvalidmobileno = isvalidmobilekey = isvaliddate = isvalidforwardemployee = isvalidbackwardemployee = 0;
    
    PNotify.removeAll();
  
    if(mobileno == "" || mobileno==null){
        $("#mobileno_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter mobile no. !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobileno = 0;
    }else {
      isvalidmobileno = 1;
    }

    if(mobilekey == "" || mobilekey==null){
        $("#mobilekey_div").addClass("has-error is-focused");
        new PNotify({title: "Please enter mobile key !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidmobilekey = 0;
    }else {
      isvalidmobilekey = 1;
    }

    if(forwardemployee==0 || forwardemployee==null) {       
      $("#forwardemployee_div").addClass("has-error is-focused");
      new PNotify({title: "Please select assign to for forward inquiry !",styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidforwardemployee = 0;
    }else{
      isvalidforwardemployee = 1;       
    }

    if(synchronize.checked == true) {
      if(todate == "" && backwardemployee==0){
        $("#date_div").addClass("has-error is-focused");
        new PNotify({title: "Please select end datetime and assign to for backward inquiry !",styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddate = 0;
      }else{
        isvaliddate = 1;
      }
    }
           
    if(isvalidmobileno==1 && isvalidmobilekey==1 && isvalidforwardemployee==1){
      
        var formData = new FormData($('#indiamartleadform')[0]);
        var uurl = SITE_URL+"external-lead/add-indiamart-lead";
        
        if(isvaliddate==1){         
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
            
                  new PNotify({title: "Key successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                  setTimeout(function() { window.location=SITE_URL+"external-lead"; }, 1500);
              },
              error: function(xhr) {              
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
                    new PNotify({title: "Key successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"external-lead"; }, 1500);
                },
                error: function(xhr) {          
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
  
  