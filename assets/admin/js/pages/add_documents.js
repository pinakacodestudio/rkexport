$(document).ready(function() {

    $("#old_document_file_div").hide();
  
    $("#remove_old_document_file").click(function() {
      $("#remove_document_file").val(1);
      $("#old_document_file_div").show();
      $("#document_file_download_div").hide();
    });
});
function resetdata(){
  
    $("#name_div").removeClass("has-error is-focused");
    $("#description_div").removeClass("has-error is-focused");
    $("#document_file_div").removeClass("has-error is-focused");
  
    if(ACTION==0){
      $('#name').val('');
      $("#description").val("");
    }
   
    $('html, body').animate({scrollTop:0},'slow');
    
}
function checkvalidation(){
    
    var name = $("#name").val().trim();
    var description = $("#description").val().trim();
    var document_file = $('input[name="document_file"]').val();
    var isvalidname  = isvaliddescription = isvaliddocument_file = 0 ;
    var remove_document_file = $("#remove_document_file").val();
    
    PNotify.removeAll();
    if(name == ''){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter document name !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else {
      if(name.length<3){
        $("#name_div").addClass("has-error is-focused");
        new PNotify({title: 'Document name require minimum 3 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvalidname = 0;
      }else{
        isvalidname = 1;
      }
    }
  
    if(ACTION==1){
      if(document_file == '' && remove_document_file==1){
        $("#document_file_div").addClass("has-error is-focused");
        new PNotify({title: 'Please select document file !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddocument_file = 0;
      }else {
          isvaliddocument_file = 1;
      }
    }
    else{
        if(document_file == ''){
          $("#document_file_div").addClass("has-error is-focused");
          new PNotify({title: 'Please select document file !',styling: 'fontawesome',delay: '3000',type: 'error'});
          isvaliddocument_file = 0;
        }else {
            isvaliddocument_file = 1;
        }
    }
  
    if(description == ''){
      $("#description_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter description !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvaliddescription = 0;
    }else {
      if(description.length<2)
      {
        $("#description_div").addClass("has-error is-focused");
        new PNotify({title: 'Description require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
        isvaliddescription = 0;
      }
      else
      {
        isvaliddescription = 1;
      }
    }
  
    if(isvalidname == 1 && isvaliddescription == 1 && isvaliddocument_file==1) {
  
      var formData = new FormData($('#documentsform')[0]);
      if(ACTION==0){
        var uurl = SITE_URL+"documents/add-documents";
        
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
              new PNotify({title: "Document successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"documents"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Document name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else if(response==3){
                new PNotify({title: "Document not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#name_div").addClass("has-error is-focused");
            }else if(response==4){
                new PNotify({title: "Document type does not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#name_div").addClass("has-error is-focused");
            }else{
              new PNotify({title: 'Document not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
        var uurl = SITE_URL+"documents/update-documents";
        
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
                new PNotify({title: "Document successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
                setTimeout(function() { window.location=SITE_URL+"documents"; }, 1500);
            }else if(response==2){
              new PNotify({title: "Document name already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
              $("#name_div").addClass("has-error is-focused");
            }else if(response==3){
                new PNotify({title: "Document not uploaded !",styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#name_div").addClass("has-error is-focused");
            }else if(response==4){
                new PNotify({title: "Document type does not valid !",styling: 'fontawesome',delay: '3000',type: 'error'});
                $("#name_div").addClass("has-error is-focused");
            }else{
                new PNotify({title: 'Document not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
  
  
  