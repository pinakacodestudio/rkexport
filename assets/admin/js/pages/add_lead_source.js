$(document).ready(function(){
    $("#color").minicolors({
      control: $(this).attr('data-control') || 'hue',
      defaultValue: $(this).attr('data-defaultValue') || '',
      format: $(this).attr('data-format') || 'hex',
      keywords: $(this).attr('data-keywords') || '',
      inline: $(this).attr('data-inline') === 'true',
      letterCase: $(this).attr('data-letterCase') || 'lowercase',
      opacity: $(this).attr('data-opacity'),
      position: $(this).attr('data-position') || 'bottom',
      swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
      change: function(value, opacity) {
        if( !value ) return;
        if( opacity ) value += ', ' + opacity;
        if( typeof console === 'object' ) {
          console.log(value);
        }
      },
      theme: 'bootstrap'
    });
})
  
function resetdata(){
  
    $("#name_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
      $('#name').val('');
    }
    $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
    
    var name = $("#name").val().trim();
    var color = $("#color").val().trim();
    
    var isvalidname  = isvalidcolor = 0 ;
    
    PNotify.removeAll();
    if(name == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter lead source name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else {
      if(name.length<3){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Lead source name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }
  
    if(color == ''){
        $("#color_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select color !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidcolor = 0;
    }else {
        isvalidcolor = 1;
    }
  
    if(isvalidname == 1 && isvalidcolor==1)
    {
  
        var formData = new FormData($('#leadsourceform')[0]);
        if(ACTION==0){
            var uurl = SITE_URL+"lead-source/add-lead-source";
            
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
                    new PNotify({title: "Lead source successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
                    setTimeout(function() { window.location=SITE_URL+"lead-source"; }, 1500);
                }else if(response==2){
                    new PNotify({title: "Lead source name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
                    $("#name_div").addClass("has-error is-focused");
                }else{
                    new PNotify({title: 'Lead source not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
            var uurl = SITE_URL+"lead-source/update-lead-source";
            
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
                        new PNotify({title: "Lead source successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                        setTimeout(function() { window.location=SITE_URL+"lead-source"; }, 1500);
                    }else if(response==2){
                        new PNotify({title: "Lead source name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
                        $("#name_div").addClass("has-error is-focused");
                    }else{
                        new PNotify({title: 'Lead source not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  
  