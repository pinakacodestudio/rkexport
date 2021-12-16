$(document).ready(function(){
  resetdata();
});
function resetdata() {

  $("#name_div").removeClass("has-error is-focused");
  $("#description_div").removeClass("has-error is-focused");
  
  if (ACTION == 1) {
    $('#name').focus();
    $('#name_div').addClass('is-focused');
  } else {
    $("#name").val('').focus();
    $("#description").val('');
    $('#name_div').addClass('is-focused');
   
    $('#yes').prop("checked", true);
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}
function checkvalidation(addtype = 0) {

  var name = $("#name").val().trim();
  var description = $("#description").val().trim();

  var isvalidname = isvaliddescription = 1;

  PNotify.removeAll();
  if (name == '') {
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter document type !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  } else if (name.length < 2) {
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Document type require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  } else{
    $("#name_div").removeClass("has-error is-focused");
  }
  
  if (description != '' && description.length<2) {
    $("#description_div").addClass("has-error is-focused");
    new PNotify({title: 'Description require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvaliddescription = 0;
  }else {
    $("#description_div").removeClass("has-error is-focused");
  }

  if (isvalidname == 1 && isvaliddescription == 1) {

    var formData = new FormData($('#form-documenttype')[0]);
    if (ACTION == 0) {
      var uurl = SITE_URL + "document-type/document-type-add";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function () {
          $('.mask').show();
          $('#loader').show();
        },
        success: function (response) {
          if (response == 1) {
            new PNotify({
              title: "Document type successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'
            });
            if (addtype == 1) {
              resetdata();
            } else {
              setTimeout(function () {window.location = SITE_URL + "document-type";}, 1500);
            }
          } else if (response == 2) {
            new PNotify({title: "Document type already exists.",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          } else {
            new PNotify({title: 'Document type not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function (xhr) {
          //alert(xhr.responseText);
        },
        complete: function () {
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    } else {

      var uurl = SITE_URL + "document-type/update-document-type";

      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function () {
          $('.mask').show();
          $('#loader').show();
        },
        success: function (response) {
          if (response == 1) {
            new PNotify({title: "Document type successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
            if (addtype == 1) {
              setTimeout(function () { window.location = SITE_URL + "document-type/add-document-type/";  }, 1500);
            } else {
              setTimeout(function () {window.location = SITE_URL + "document-type";}, 1500);
            }
          } else if (response == 2) {
            new PNotify({title: "Document type already exists !",styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#name_div").addClass("has-error is-focused");
          } else {
            new PNotify({title: 'Document type not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function (xhr) {
          //alert(xhr.responseText);
        },
        complete: function () {
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

