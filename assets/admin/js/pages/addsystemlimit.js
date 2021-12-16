$(document).ready(function() {
	getallow();
});
function resetdata(){
  
  $("#fcmkey_div").removeClass("has-error is-focused");

  $('html, body').animate({scrollTop:0},'slow'); 
}
function checkvalidation(){
  
  var fcmkey = $("#fcmkey").val();
  var branding = $("#branding").val();
  var brandingallow = $("#brandingallow").val();

  var isvalidfcmkey = isvalidfcmkey = 0;

	if($("#pioneeredby").prop('checked') == true){
		branding = '0';
	}else{
		branding = '1';
	}
			
	if($("#brandingallow").prop('checked') == true){
		brandingallow = '1';
	}else{
		brandingallow = '0';
	}

  PNotify.removeAll();
 
  if(fcmkey.trim() == ''){
    $("#fcmkey_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter fcmkey !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidfcmkey = 0;
  }else { 
    isvalidfcmkey = 1;
  }
 
  if(isvalidfcmkey == 1){
    
      var uurl = SITE_URL+"systemlimit/setsystemlimit";
      var formData = new FormData($('#formsystemlimit')[0]);
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
            new PNotify({title: "System limit successfully set.",styling: 'fontawesome',delay: '3000',type: 'success'});
            setTimeout(function() { window.location.reload(); }, 1500);
          }else{
            new PNotify({title: 'System limit not set !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

function getallow(){
			 var input =$("#brandingallow").val();
	if($("#brandingallow").prop('checked') == true){
		document.getElementById("pioneeredby").disabled = false;
		document.getElementById("poweredby").disabled = false;

		 var a = input.checked ? "1" : "0";
		document.getElementById('brandingallow').val = a;
	}else{
		document.getElementById("pioneeredby").disabled = true;
		document.getElementById("poweredby").disabled = true;
		var a = input.checked ? "1" : "0";
		document.getElementById('brandingallow').val = a;
	}
}

