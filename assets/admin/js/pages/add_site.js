$(document).ready(function(){
  resetdata();

  getprovince($('#countryid').val());
  getcity($('#provinceid').val());

  $('#countryid').on('change', function (e) {

    $('#provinceid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select Province</option>')
      .val('0');
    $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select City</option>')
      .val('0');
    $('#provinceid').selectpicker('refresh');
    $('#cityid').selectpicker('refresh');
    getprovince(this.value);
  });
  $('#provinceid').on('change', function (e) {

    $('#cityid')
      .find('option')
      .remove()
      .end()
      .append('<option value="">Select City</option>')
      .val('0');
    $('#cityid').selectpicker('refresh');
    getcity(this.value);
  });
});
function resetdata() {

  $("#sitename_div").removeClass("has-error is-focused");
  $("#sitemanager_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#country_div").removeClass("has-error is-focused");
  $("#province_div").removeClass("has-error is-focused");
  $("#city_div").removeClass("has-error is-focused");
  
  if (ACTION == 1) {
    $('#sitename').focus();
    $('#sitename_div').addClass('is-focused');
  } else {
    $("#sitename").val('').focus();
    $('#sitename_div').addClass('is-focused');
    $("#sitemanagerid,#address").val('');
    $("#countryid").val(countryid).selectpicker("refresh").change();
    getprovince(countryid);

    $('#yes').prop("checked", true);
    $(".selectpicker").selectpicker("refresh");
  }
  $('html, body').animate({scrollTop: 0}, 'slow');
}

function checkvalidation(addtype = 0) {

  var sitename = $("#sitename").val().trim();
  var sitemanagerid = $("#sitemanagerid").val();
  var address = $("#address").val().trim();
  var countryid = $("#countryid").val();
  var provinceid = $("#provinceid").val();
  var cityid = $("#cityid").val();

  var isvalidsitename = isvalidsitemanagerid = isvalidaddress = isvalidcountryid = isvalidprovinceid = isvalidcityid = 0;

  PNotify.removeAll();
  if (sitename == '') {
    $("#sitename_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter site name !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else if (sitename.length < 2) {
    $("#sitename_div").addClass("has-error is-focused");
    new PNotify({title: 'Site name require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
    $("#sitename_div").removeClass("has-error is-focused");
    isvalidsitename = 1;
  }

  if (sitemanagerid == null) {
    $("#sitemanager_div").addClass("has-error is-focused");
    new PNotify({ title: 'Please select site manager !', styling: 'fontawesome', delay: '3000', type: 'error' });
  } else {
    $("#sitemanager_div").removeClass("has-error is-focused");
    isvalidsitemanagerid = 1;
  }

  if (address == '') {
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else if (address.length < 2) {
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: 'Address require minimum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
  } else {
    $("#address_div").removeClass("has-error is-focused");
    isvalidaddress = 1;
  }

  if (countryid == 0) {
    $("#country_div").addClass("has-error is-focused");
    new PNotify({ title: 'Please select country !', styling: 'fontawesome', delay: '3000', type: 'error' });
  } else {
    $("#country_div").removeClass("has-error is-focused");
    isvalidcountryid = 1;
  }
  if (provinceid == 0) {
    $("#province_div").addClass("has-error is-focused");
    new PNotify({ title: 'Please select province !', styling: 'fontawesome', delay: '3000', type: 'error' });
  } else {
    $("#province_div").removeClass("has-error is-focused");
    isvalidprovinceid = 1;
  }
  if (cityid == 0) {
    $("#city_div").addClass("has-error is-focused");
    new PNotify({ title: 'Please select city !', styling: 'fontawesome', delay: '3000', type: 'error' });
  } else {
    $("#city_div").removeClass("has-error is-focused");
    isvalidcityid = 1;
  }

  if (isvalidsitename == 1 && isvalidsitemanagerid == 1 && isvalidaddress == 1 && isvalidcountryid == 1 && isvalidprovinceid == 1 && isvalidcityid== 1) {

    var formData = new FormData($('#siteform')[0]);
    if (ACTION == 0) {
      var uurl = SITE_URL + "site/site-add";

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
            new PNotify({ title: "Site successfully added.", styling: 'fontawesome', delay: '3000', type: 'success' });
            if(addtype==1) {
              resetdata();
            } else{
              setTimeout(function () { window.location = SITE_URL + "site"; }, 1500); 
            }
          } else if (response == 2) {
            new PNotify({ title: "Site name already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
            $("#sitename_div").addClass("has-error is-focused");
          } else {
            new PNotify({ title: 'Site not added !', styling: 'fontawesome', delay: '3000', type: 'error' });
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

      var uurl = SITE_URL + "site/update-site";

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
            new PNotify({title: "Site successfully updated.", styling: 'fontawesome', delay: '1500', type: 'success' });
            if(addtype==1) {
              setTimeout(function() {window.location=SITE_URL+"site/add-site"; }, 1500);
            } else{
              setTimeout(function () { window.location = SITE_URL + "site"; }, 1500); 
            }
          } else if (response == 2) {
            new PNotify({ title: "Site name already exists !", styling: 'fontawesome', delay: '3000', type: 'error' });
            $("#sitename_div").addClass("has-error is-focused");
          } else {
            new PNotify({ title: 'Site not updated !',styling: 'fontawesome', delay: '3000', type: 'error' });
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